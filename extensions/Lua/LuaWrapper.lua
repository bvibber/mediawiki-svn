#!/usr/bin/env lua
-- Lua parser extensions for MediaWiki - Wrapper for Lua interpreter
--
-- @author Fran Rogers
-- @package MediaWiki
-- @addtogroup Extensions
-- @license See 'COPYING'
-- @file

function make_sandbox()
  local function dummy(...)
    return nil
  end

  local function deepcopy(object, override)
    local lookup_table = {}
    local function _copy(object, override)
      if type(object) ~= "table" then
	return object
      elseif lookup_table[object] then
	return lookup_table[object]
      end
      local new_table = {}
      lookup_table[object] = new_table
      for index, value in pairs(object) do
	if override ~= nil then
	  value = override
	end
	new_table[_copy(index)] = _copy(value, override)
      end
      return setmetatable(new_table, _copy(getmetatable(object), override))
    end
    return _copy(object, override)
  end

  local env = {}

    local function _escape(s)
      s = string.gsub(s, "\\", "\\\\")
      s = string.gsub(s, "\'", "\\\'")
      return s
    end

  env._OUTPUT = ""

  local function writewrapper(...)
    local out = ""
    for n = 1, select("#", ...) do
      if out == "" then
	out = tostring(select(n, ...))
      else
	out = out .. tostring(select(n, ...))
      end
    end
    env._OUTPUT = env._OUTPUT .. out
  end

  local function outputwrapper(file)
    if file == nil then
      local file = {}
      file.close = dummy
      file.lines = dummy
      file.read = dummy
      file.flush = dummy
      file.seek = dummy
      file.setvbuf = dummy
      function file:write(...) writewrapper(...); end
      return file
    else
      return nil
    end
  end

  local function printwrapper(...)
    local out = ""
    for n = 1, select("#", ...) do
      if out == "" then
	out = tostring(select(n, ...))
      else
	out = out .. '\t' .. tostring(select(n, ...))
      end
    end
    env._OUTPUT =env._OUTPUT .. out .. "\n"
  end

  local oldloadstring = loadstring
  local function safeloadstring(s, chunkname)
    local f, message = oldloadstring(s, chunkname)
    if not f then
      return f, message
    end
    setfenv(f, getfenv(2))
    return f
  end

  env.assert = _G.assert
  env.error = _G.error
  env._G = env
  env.ipairs = _G.ipairs
  env.loadstring = safeloadstring
  env.next = _G.next
  env.pairs = _G.pairs
  env.pcall = _G.pcall
  env.print = printwrapper
  env.write = writewrapper
  env.select = _G.select
  env.tonumber = _G.tonumber
  env.tostring = _G.tostring
  env.type = _G.type
  env.unpack = _G.unpack
  env._VERSION = _G._VERSION
  env.xpcall = _G.xpcall
  env.coroutine = deepcopy(_G.coroutine)
  env.string = deepcopy(_G.string)
  env.string.dump = nil
  env.table = deepcopy(_G.table)
  env.math = deepcopy(_G.math)
  env.io = {}
  env.io.write = writewrapper
  env.io.flush = dummy
  env.io.type = typewrapper
  env.io.output = outputwrapper
  env.io.stdout = outputwrapper()
  env.os = {}
  env.os.clock = _G.os.clock
  -- env.os.date = _G.os.date
  env.os.difftime = _G.os.difftime
  env.os.time = _G.os.time

  return env
end

function make_hook(maxlines, maxcalls, diefunc)
  local lines = 0
  local calls = 0
  function _hook(event, ...)
    if event == "call" then
      calls = calls + 1
      if calls > maxcalls then
	diefunc("RECURSION_LIMIT")
      end
    elseif event == "return" then
      calls = calls - 1
    elseif event == "line" then
      lines = lines + 1
      if lines > maxlines then
	diefunc("LOC_LIMIT")
      end
    end
  end
  return _hook
end

function wrap(chunk, env, hook)
  local err = nil
  env._OUTPUT = ""
  setfenv(chunk, env)
  debug.sethook(hook, "crl")
  res = xpcall(chunk, function(s) err = s; end)
  debug.sethook()
  return res, err
end

function main()
  if #arg ~= 2 then
    io.stderr:write(string.format("usage: %s MAXLINES MAXCALLS\n", arg[0]))
    os.exit(1)
  end

  io.stdout:setvbuf("no")
  function _die(reason)
    io.stdout:write("'", reason, "', false\n.\n")
    os.exit(1)
  end
  hook = make_hook(tonumber(arg[1]), tonumber(arg[2]),
		   _die)
  local env = make_sandbox()
  while true do
    local chunkstr = ""
    while true do
      local line = io.stdin:read("*l")
      if chunkstr == "" and line == nil then
	return nil
      elseif line == "." or line == nil then
	break
      elseif chunkstr ~= "" then
	chunkstr = chunkstr .. "\n" .. line
      else
	chunkstr = line
      end
    end

    local chunk
    local err
    chunk, err = loadstring(chunkstr)

    if err == nil then
      local res
      res, err = wrap(chunk, env, hook)
    end

    if err == nil then
      io.stdout:write("'", env._OUTPUT, "', true\n.\n")
    else
      io.stdout:write("'", err, "', false\n.\n")
    end
  end
  exit(0)
end

if arg ~= nil then
  main()
end
